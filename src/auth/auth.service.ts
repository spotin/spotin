import { Injectable, UnauthorizedException } from '@nestjs/common';
import { UsersService } from '@/users/users.service';
import { LoginUser } from '@/auth/email-password/types/login-user.type';
import { SpotsService } from '@/spots/spots.service';
import * as argon2id from 'argon2';
import * as crypto from 'crypto';
import { User } from '@/users/types/user';
import { EnvironmentVariables } from '@/config/config.constants';
import { ConfigService } from '@nestjs/config';

@Injectable()
export class AuthService {
	constructor(
		private readonly configService: ConfigService<EnvironmentVariables, true>,
		private readonly spotsService: SpotsService,
		private readonly usersService: UsersService,
	) {}

	async validateCredentials({ email, password }: LoginUser): Promise<User> {
		const user = await this.usersService.getUserByEmail(email);

		const passwordsMatch = await argon2id.verify(user.password, password);

		if (!user.enabled || !passwordsMatch) {
			throw new UnauthorizedException();
		}

		return user;
	}

	async validateSession(userId: string): Promise<User> {
		const user = await this.usersService.getUser(userId);

		if (!user || !user.enabled) {
			throw new UnauthorizedException();
		}

		return user;
	}

	async validateToken(value: string): Promise<User> {
		const hash = crypto.createHash('sha256').update(value).digest('hex');

		const user = await this.usersService.getUserByTokenHash(hash);

		if (!user.enabled) {
			throw new UnauthorizedException();
		}

		return user;
	}

	async validateSpot(spotId: string): Promise<User> {
		const spot = await this.spotsService.getSpot(spotId);

		if (spot.configured) {
			throw new UnauthorizedException();
		}

		const spotWithUser = await this.spotsService.getSpotWithUser(spot.id);

		return spotWithUser.user;
	}

	async validatePasswordResetRequestToken(token: string): Promise<User> {
		const user =
			await this.usersService.getUserByResetPasswordRequestToken(token);

		if (!user.enabled) {
			throw new UnauthorizedException();
		}

		return user;
	}
}
