import { Injectable, UnauthorizedException } from '@nestjs/common';
import { JwtService } from '@nestjs/jwt';
import { UsersService } from '@/users/users.service';
import { LoginUser } from '@/auth/local/types/login-user.type';
import { JwtPayload } from '@/auth/jwt/types/jwt-payload.type';
import { SpotsService } from '@/spots/spots.service';
import * as argon2id from 'argon2';
import * as crypto from 'crypto';
import { Jwt } from '@/auth/jwt/types/jwt.type';
import { User } from '@/users/types/user';
import {
	EnvironmentVariables,
	JWT_ACCESS_TOKEN_EXPIRATION_TIME,
	JWT_ACCESS_TOKEN_SECRET,
	JWT_REFRESH_TOKEN_EXPIRATION_TIME,
	JWT_REFRESH_TOKEN_SECRET,
} from '@/config/config.constants';
import { ConfigService } from '@nestjs/config';

@Injectable()
export class AuthService {
	constructor(
		private readonly configService: ConfigService<EnvironmentVariables, true>,
		private readonly jwtService: JwtService,
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

	async generateJwtTokens(userId: string): Promise<Jwt> {
		const payload: JwtPayload = {
			sub: userId,
		};

		const [accessToken, refreshToken] = await Promise.all([
			this.jwtService.signAsync(payload, {
				secret: this.configService.get(JWT_ACCESS_TOKEN_SECRET, {
					infer: true,
				}),
				expiresIn: this.configService.get(JWT_ACCESS_TOKEN_EXPIRATION_TIME, {
					infer: true,
				}),
			}),
			this.jwtService.signAsync(payload, {
				secret: this.configService.get(JWT_REFRESH_TOKEN_SECRET, {
					infer: true,
				}),
				expiresIn: this.configService.get(JWT_REFRESH_TOKEN_EXPIRATION_TIME, {
					infer: true,
				}),
			}),
		]);

		await this.usersService.updateUser(userId, {
			refreshToken: await argon2id.hash(refreshToken),
		});

		return {
			accessToken,
			refreshToken,
		};
	}

	async validateJwtRefreshToken(
		userId: string,
		refreshToken: string,
	): Promise<User> {
		const user = await this.usersService.getUser(userId);

		const refreshTokenMatches = await argon2id.verify(
			user.refreshToken as string,
			refreshToken,
		);

		if (!user.enabled || !refreshTokenMatches) {
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
