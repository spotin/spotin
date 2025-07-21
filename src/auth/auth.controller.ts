import {
	Controller,
	Post,
	Body,
	HttpCode,
	Res,
	ConflictException,
} from '@nestjs/common';
import {
	ApiBody,
	ApiConflictResponse,
	ApiOkResponse,
	ApiOperation,
	ApiTags,
} from '@nestjs/swagger';
import { AuthService } from '@/auth/auth.service';
import { LoginUserDto } from '@/auth/local/dtos/login-user.dto';
import { UsersService } from '@/users/users.service';
import { LocalAuth } from '@/auth/local/local-auth.decorator';
import { JwtDto } from '@/auth/jwt/dtos/jwt.dto';
import { AuthJwtPayload } from '@/auth/decorators/auth-jwt-payload.decorator';
import { RegisterUserDto } from '@/auth/local/dtos/register-user.dto';
import { ResetPasswordAuth } from '@/auth/reset-password/reset-password.decorator';
import { ResetPasswordDto } from '@/auth/reset-password/dtos/reset-password.dto';
import { ResetPasswordRequestsService } from '@/reset-password-requests/reset-password-requests.service';
import { ResetPasswordRequestDto } from '@/auth/reset-password/dtos/reset-password-request.dto';
import { Response } from 'express';
import { PrismaClientKnownRequestError } from '@prisma/client/runtime/library';
import { UserRole } from '@/users/enums/user-role';
import { I18n, I18nContext } from 'nestjs-i18n';
import {
	EnvironmentVariables,
	JWT_ACCESS_TOKEN_COOKIE_NAME,
	JWT_REFRESH_TOKEN_COOKIE_NAME,
	NODE_ENV,
} from '@/config/config.constants';
import { ConfigService } from '@nestjs/config';
import { JwtRefreshTokenAuth } from '@/auth/jwt/jwt-refresh-token-auth.decorator';
import { JwtPayload } from '@/auth/jwt/types/jwt-payload.type';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { User } from '@/users/types/user';

@ApiTags('Auth')
@Controller('api/auth')
export class AuthController {
	constructor(
		private readonly authService: AuthService,
		private readonly configService: ConfigService<EnvironmentVariables, true>,
		private readonly usersService: UsersService,
		private readonly resetPasswordRequestsService: ResetPasswordRequestsService,
	) {}

	private async getJwtTokens(userId: string, res: Response): Promise<JwtDto> {
		const jwtTokens = await this.authService.generateJwtTokens(userId);

		res.cookie(
			this.configService.get(JWT_ACCESS_TOKEN_COOKIE_NAME, { infer: true }),
			jwtTokens.accessToken,
			{
				httpOnly: true,
				sameSite: 'strict',
				secure:
					this.configService.get(NODE_ENV, { infer: true }) === 'production',
			},
		);

		res.cookie(
			this.configService.get(JWT_REFRESH_TOKEN_COOKIE_NAME, { infer: true }),
			jwtTokens.refreshToken,
			{
				httpOnly: true,
				sameSite: 'strict',
				path: '/api/auth/refresh',
				secure:
					this.configService.get(NODE_ENV, { infer: true }) === 'production',
			},
		);

		return new JwtDto(jwtTokens);
	}

	@Post('login')
	@LocalAuth()
	@HttpCode(200)
	@ApiOperation({
		summary: 'Log in with email and password',
		description: 'Log in with email and password.',
		operationId: 'login',
	})
	@ApiBody({
		description: "The user's credentials.",
		type: LoginUserDto,
	})
	@ApiOkResponse({
		description: 'The user has been successfully logged in.',
		type: JwtDto,
	})
	async login(
		@AuthUser() user: User,
		@Res({ passthrough: true }) res: Response,
	): Promise<JwtDto> {
		await this.resetPasswordRequestsService.deleteResetPasswordRequestForUser(
			user.id,
		);

		const jwtTokens = await this.getJwtTokens(user.id, res);

		return jwtTokens;
	}

	@Post('refresh')
	@HttpCode(200)
	@ApiOperation({
		summary: 'Refresh the JWT access token',
		description: 'Refresh the JWT access token.',
		operationId: 'refresh',
	})
	@ApiOkResponse({
		description: 'The JWT access token has been successfully refreshed.',
		type: JwtDto,
	})
	@JwtRefreshTokenAuth()
	async refresh(
		@AuthJwtPayload() payload: JwtPayload,
		@Res({ passthrough: true }) res: Response,
	): Promise<JwtDto> {
		const jwtTokens = await this.getJwtTokens(payload.sub, res);

		return jwtTokens;
	}

	@Post('register')
	@HttpCode(200)
	@ApiOperation({
		summary: 'Register a new user',
		description: 'Register a new user.',
		operationId: 'register',
	})
	@ApiBody({
		description: "The user's details.",
		type: RegisterUserDto,
	})
	@ApiOkResponse({
		description: 'The user has been successfully signed up.',
	})
	@ApiConflictResponse({
		description: 'The username is already taken.',
	})
	async register(
		@Body() registerUserDto: RegisterUserDto,
		@I18n() i18n: I18nContext,
	): Promise<void> {
		try {
			await this.usersService.createUser(
				{
					...registerUserDto,
					enabled: true,
					role: UserRole.STANDARD_USER,
				},
				i18n.lang,
			);
		} catch (e) {
			if (e instanceof PrismaClientKnownRequestError && e.code === 'P2002') {
				const meta = e.meta as Record<string, Array<string>>;

				if (meta.target.includes('username')) {
					throw new ConflictException();
				}

				try {
					const user = await this.usersService.getUserByEmail(
						registerUserDto.email,
					);

					if (user.enabled) {
						await this.resetPasswordRequestsService.sendRecoverAccountForUser(
							user,
							i18n.lang,
						);
					}
				} catch {
					// Do nothing
				}
			} else {
				throw e;
			}
		}
	}

	@Post('reset-password-request')
	@HttpCode(200)
	@ApiOperation({
		summary: 'Request password reset for user',
		description: 'Request password reset for user.',
		operationId: 'resetPasswordRequest',
	})
	@ApiBody({
		description: 'The email address of the user.',
		type: ResetPasswordRequestDto,
	})
	@ApiOkResponse({
		description:
			'The user password reset has been successfully requested. Note: if the email is inexistent, nothing will happen.',
	})
	async resetPasswordRequest(
		@Body() requestPasswordResetDto: ResetPasswordRequestDto,
		@I18n() i18n: I18nContext,
	): Promise<void> {
		try {
			const user = await this.usersService.getUserByEmail(
				requestPasswordResetDto.email,
			);

			if (user.enabled) {
				await this.resetPasswordRequestsService.sendResetPasswordRequestForUser(
					user,
					i18n.lang,
				);
			}
		} catch {
			// Do nothing
		}
	}

	@Post('reset-password')
	@ResetPasswordAuth()
	@HttpCode(200)
	@ApiOperation({
		summary: 'Reset password for user',
		description: 'Reset password for user.',
		operationId: 'resetPassword',
	})
	@ApiBody({
		description: 'The new password.',
		type: ResetPasswordDto,
	})
	@ApiOkResponse({
		description: 'The user password has been successfully reset.',
	})
	async resetPassword(
		@AuthJwtPayload() payload: JwtPayload,
		@Body() resetPasswordDto: ResetPasswordDto,
	): Promise<void> {
		await this.resetPasswordRequestsService.deleteResetPasswordRequestForUser(
			payload.sub,
		);

		await this.usersService.updateUser(payload.sub, {
			password: resetPasswordDto.password,
		});
	}
}
