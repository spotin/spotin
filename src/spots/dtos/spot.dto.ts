import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import {
	IsBoolean,
	IsDateString,
	IsNumber,
	IsString,
	IsUUID,
	Min,
	Max,
	IsUrl,
	MaxLength,
	MinLength,
	IsJSON,
	IsOptional,
} from 'class-validator';
import { Spot } from '@/spots/types/spot';
import { IsDirectAccessAllowedIfWebsiteTargetExists } from '@/spots/decorators/is-direct-access-allowed-if-website-target-exists.decorator';

export class SpotDto implements Spot {
	/**
	 * Identification of the spot
	 */
	@ApiProperty({ format: 'uuid' })
	@IsUUID()
	id: string;

	/**
	 * Name of the spot
	 */
	@ApiPropertyOptional()
	@IsOptional()
	@IsString()
	@MinLength(1)
	@MaxLength(255)
	name: string | null;

	/**
	 * Description of the spot
	 */
	@ApiPropertyOptional()
	@IsOptional()
	@IsString()
	@MinLength(1)
	description: string | null;

	/**
	 * Latitude of the position of the spot
	 */
	@ApiPropertyOptional()
	@IsOptional()
	@IsNumber()
	@Min(-90)
	@Max(90)
	latitude: number | null;

	/**
	 * Longitude of the position of the spot
	 */
	@ApiPropertyOptional()
	@IsOptional()
	@IsNumber()
	@Min(-180)
	@Max(180)
	longitude: number | null;

	/**
	 * Payload to store inconsistent data about the spot
	 */
	@ApiPropertyOptional()
	@IsOptional()
	@IsJSON()
	@MaxLength(1000)
	payload: string | null;

	/**
	 * Redirection target of the spot
	 */
	@ApiPropertyOptional({ format: 'url' })
	@IsOptional()
	@IsUrl()
	@MaxLength(255)
	websiteTarget: string | null;

	/**
	 * Set if the spot will be redirected directly to the website target
	 */
	@ApiPropertyOptional({ default: false })
	@IsOptional()
	@IsBoolean()
	@IsDirectAccessAllowedIfWebsiteTargetExists()
	directAccessToWebsiteTarget: boolean = false;

	/**
	 * Set if the spot will be public on the website
	 */
	@ApiPropertyOptional({ default: false })
	@IsOptional()
	@IsBoolean()
	public: boolean = false;

	/**
	 * Set if the spot is already configured or not
	 */
	@ApiPropertyOptional({ default: true })
	@IsOptional()
	@IsBoolean()
	configured: boolean = true;

	/**
	 * Date when the spot was created
	 */
	@IsDateString()
	createdAt: Date;

	/**
	 * Date when the spot was updated
	 */
	@IsDateString()
	updatedAt: Date;
}
